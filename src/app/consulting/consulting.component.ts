import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';

@Component({
  selector: 'app-consulting',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            MatIconModule,
            RouterOutlet, RouterLink, RouterLinkActive],
  templateUrl: './consulting.component.html',
  styleUrl: './consulting.component.css'
})
export class ConsultingComponent {

}
