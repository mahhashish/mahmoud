package org.ew.lucene;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;

/**
 * @author Dave Palmer <dave@engineworks.org>
 * 
 * In order to run this, you need to first compile it. To do so, you need
 * a version of the JDK. I would recommend getting the Sun JDK 1.4:
 * http://java.sun.com
 */
public class IndexEngine {

	public static void main(String[] args) throws Exception {
		System.out.println("Preparing to index links database...");
		index(getConnection());
		System.out.println("Index complete");
	}

	private static void index(Connection conn) throws Exception {
		
		// this is the query we are going to use to populate our index
		String sql = "select lid,name,url,description from links";
		
		// this is where you want to store your index. This is just
		// and full path statement.
		String indexPath = "/path/to/index/file";
		
		Analyzer analyzer = new StandardAnalyzer();
		IndexWriter writer = new IndexWriter(indexPath,analyzer,true);
		PreparedStatement pStmt = conn.prepareStatement(sql);
		
		System.out.println("Executing query...");
		
		ResultSet rs = pStmt.executeQuery();

		int count = 0;
		int interval = 250;
		long timeout = 50;
		
		System.out.println("Preparing to build index...");
		
		while (rs.next()) {
			if (count == interval) {
				java.lang.Thread.sleep(timeout);
				count = 0;
			} else {
				count++;
			}
			
			System.out.println("Adding link: " + rs.getString("name"));
			
			Document d = new Document();
			
			// adding our columns to our Lucene Document object
			d.add(Field.Text("lid", rs.getString("lid")));
			d.add(Field.Text("name",rs.getString("name")));
			d.add(Field.Text("url",rs.getString("url")));
			d.add(Field.Text("description", rs.getString("description")));
			
			// adding our document object instance to our writer
			writer.addDocument(d);
		}
		
		writer.close();
	}

	private static Connection getConnection() throws Exception {
		Class.forName("com.mysql.jdbc.Driver").newInstance();
		
		// set the name of your MySQL host and the name of the database you
		// want to use
		String url = "jdbc:mysql://db_host/db_name";
		
		// the user name you need to use to log in to your MySQL DB
		String user = "db_user";
		
		// and of course the password.
		String pass = "db_pass";

		System.out.println("Preparing connection with URL: " + url);
		System.out.println("database user: " + user);
		return DriverManager.getConnection(url, user, pass);
	}
}
